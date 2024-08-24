<?php namespace App\Games\Kernel\ThirdParty\EvoPlay;

use App\Currency\Currency;
use App\Games\Kernel\Data;
use App\Games\Kernel\GameCategory;
use App\Games\Kernel\Metadata;
use App\Games\Kernel\ThirdParty\ThirdPartyGame;
use App\Leaderboard;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class EvoPlayGame
 *
 * Demo details:
 * server: api.8provider.com
 * project id: 1777
 * secret key: a8d89dff87dd81dfa4d2f828dca679a5
 *
 * @package App\Games\Kernel\ThirdParty\EvoPlay
 */
class EvoPlayGame extends ThirdPartyGame {

    private string $server;

    private string $project_id;
    private string $secret_key;

    private string $version = '1';
    private string $callback_version = '2';

    public function __construct(?array $data = null) {
        parent::__construct($data);

        $this->project_id = '1777';
        $this->secret_key = 'a8d89dff87dd81dfa4d2f828dca679a5';
        $this->server = 'a8d89dff87dd81dfa4d2f828dca679a5';
    }

    public function provider(): string {
        return "evo";
    }

    function metadata(): Metadata {
        return new class($this->data) extends Metadata {
            private ?array $data;

            public function __construct(?array $data) {
                $this->data = $data;
            }

            function id(): string {
                return "evo:".(!$this->data ? 'dummy' : $this->data['id']);
            }

            function name(): string {
                return $this->data ? $this->data['name'] : 'Dummy';
            }

            function icon(): string {
                $path = str_replace(' ', '', str_replace('\\', '/', mb_substr($this->data['absolute_name'], strrpos($this->data['absolute_name'], '\\'))));
                return !$this->data ? "fal fa-question-circle" : '/img/assets/evo/'.$path.'.jpg';
            }

            public function category(): array {
                if(!$this->data) return [GameCategory::$evo_play];

                $categories = [];

                array_push($categories, GameCategory::$evo_play);
                if($this->data['game_sub_type'] === 'Slot') array_push($categories, GameCategory::$slots);
                else if($this->data['game_sub_type'] === 'Instant') array_push($categories, GameCategory::$instant);
                else if($this->data['game_sub_type'] === 'Blackjack') array_push($categories, GameCategory::$table);
                else if($this->data['game_sub_type'] === 'Poker') array_push($categories, GameCategory::$table);
                else if($this->data['game_sub_type'] === 'Roulette') array_push($categories, GameCategory::$roulette);
                else if($this->data['game_sub_type'] === 'socketgames') array_push($categories, GameCategory::$multiplayer);
                else if($this->data['game_sub_type'] === 'table') array_push($categories, GameCategory::$table);

                return array_merge($categories);
            }
        };
    }

    function processCallback(Request $request): array {
        $instance = \App\Game::where('id', $request->token)->first();
        $currency = Currency::find($instance->currency);
        $user = \App\User::where('_id', $instance->user)->first();

        if(!$user || !$currency || $currency->tokenPrice() == -1) return [ 'status' => 'error', 'no_refund' => '1' ];

        switch ($request->name) {
            case "init":
                return [
                    'status' => 'ok',
                    'data' => [
                        'balance' => $currency->convertTokenToUSD($user->balance($currency)->demo($instance->demo)->get()),
                        'currency' => 'USD'
                    ]
                ];
            case "bet":
                $balance = $user->balance($currency)->demo($instance->demo)->get();

                if($currency->convertTokenToUSD($balance) < floatval($request->data['amount'])) return [
                    'status' => 'error',
                    'balance' => $currency->convertTokenToUSD($balance),
                    'currency' => 'USD',
                    'message' => 'Not enough money',
                    'no_refund' => '1'
                ];

                $user->balance($currency)->demo($instance->demo)->subtract($currency->convertUSDToToken(floatval($request->data['amount'])));

                return [
                    'status' => 'ok',
                    'data' => [
                        'balance' => $currency->convertTokenToUSD($user->balance($currency)->demo($instance->demo)->get()),
                        'currency' => 'USD'
                    ]
                ];
            case "win":
                if(floatval($request->data['amount']) > 0) $user->balance($currency)->demo($instance->demo)->add($currency->convertUSDToToken(floatval($request->data['amount'])));

                try {
                    if (!$instance->demo && isset($request->data['details']['total_bet'])) {
                        $game = \App\Game::create([
                            'id' => DB::table('games')->count() + 1,
                            'user' => $instance->user,
                            'game' => $this->metadata()->id(),
                            'wager' => $currency->convertUSDToToken(floatval($request->data['details']['total_bet'] ?? 0)),
                            'multiplier' => floatval($request->data['amount']) === 0 ? 0 : floatval($request->data['amount']) / floatval($request->data['details']['total_bet']),
                            'status' => floatval($request->data['amount']) > 0 ? 'win' : 'lose',
                            'profit' => $currency->convertUSDToToken(floatval($request->data['amount'])),
                            'type' => 'third-party',
                            'currency' => $instance->currency
                        ]);

                        event(new \App\Events\LiveFeedGame($game, 0));
                        Leaderboard::insert($game);
                    }
                } catch (\Exception $e) {}

                return [
                    'status' => 'ok',
                    'data' => [
                        'balance' => $currency->convertTokenToUSD($user->balance($currency)->demo($instance->demo)->get()),
                        'currency' => 'USD'
                    ]
                ];
            default:
                return [
                    'status' => 'error'
                ];
        }
    }

    function process(Data $data) {
        $gameId = uniqid();

        $response = $this->sendRequest('Game/getURL', [
            'token' => $data->demo() ? 'demo' : $gameId,
            'game' => intval(substr($data->id(), strlen('evo:'))),
            'settings' => [
                'user_id' => $data->user() ? $data->user()->_id : 'guest',
                'exit_url' => url('/'),
                'cash_url' => url('/wallet'),
                'language' => 'en',
                'https' => '1'
            ],
            'denomination' => '1',
            'currency' => 'USD',
            'return_url_info' => '1',
            'callback_version' => $this->callback_version
        ]);

        $game = \App\Game::create([
            'id' => $gameId,
            'user' => $data->guest() ? null : $data->user()->_id,
            'game' => $this->metadata()->id(),
            'wager' => $data->bet(),
            'status' => 'in-progress',
            'profit' => 0,
            'nonce' => $this->nonce(),
            'multiplier' => 0,
            'currency' => $data->currency(),
            'data' => [
                'link' => $response['data']['link']
                //'session_id' => $response['data']['session_id']
            ],
            'demo' => $data->demo(),
            'type' => 'third-party'
        ]);

        return [
            'response' => [
                'id' => $game->_id,
                'wager' => $data->bet(),
                'type' => 'third-party',
                'link' => $response['data']['link']
            ]
        ];
    }

    public function createInstances(): array {
        $games = $this->sendRequest('Game/getList', ['need_extra_data' => 1], true);

        $instances = [];
        foreach($games['data'] as $id => $game) {
            array_push($instances, new EvoPlayGame(array_merge([
                'id' => $id
            ], $game)));
        }

        return $instances;
    }

    private function sendRequest(string $type, array $args = [], bool $cache = false): array {
        $cacheKey = 'evo:'.$type.':'.http_build_query($args);
        if($cache && Cache::has($cacheKey)) return Cache::get($cacheKey);

        $response = json_decode(file_get_contents('https://'.$this->server.'/'.$type.'?'.http_build_query(array_merge([
                'project' => $this->project_id,
                'version' => $this->version,
                'signature' => $this->generateSignature($this->version, $args)
            ], $args))), true);

        if($cache) Cache::put($cacheKey, $response, now()->addDays(1));
        return $response;
    }

    private function generateSignature($version, array $args) {
        $md5 = [];
        $md5[] = $this->project_id;
        $md5[] = $version;
        foreach ($args as $required_arg) {
            $arg = $required_arg;
            if(is_array($arg)) {
                if(count($arg)) {
                    $recursive_arg = '';
                    array_walk_recursive($arg, function($item) use (&$recursive_arg) { if(!is_array($item)) { $recursive_arg .= ($item . ':'); } });
                    $md5[] = substr($recursive_arg, 0, strlen($recursive_arg) - 1);
                } else $md5[] = '';
            } else $md5[] = $arg;
        }
        $md5[] = $this->secret_key;
        $md5_str = implode('*', $md5);
        $md5 = md5($md5_str);
        return $md5;
    }

}
