<?php namespace App\Games\Kernel\ThirdParty\Habanero;

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
 * Class HabaneroGame
 * Warning: demo games only!
 *
 * @package App\Games\Kernel\ThirdParty\Habanero
 */
class HabaneroGame extends ThirdPartyGame {

    public function provider(): string {
        return "habanero";
    }

    function metadata(): Metadata {
        return new class($this->data) extends Metadata {
            private ?array $data;

            public function __construct(?array $data) {
                $this->data = $data;
            }

            function id(): string {
                return "habanero:".(!$this->data ? 'dummy' : $this->data['id']);
            }

            function name(): string {
                return $this->data ? $this->data['Name'] : 'Dummy';
            }

            function icon(): string {
                return !$this->data ? '' : "https://app-test.insvr.com/img/rectangle/425/{$this->data['KeyName']}.png";
            }

            public function category(): array {
                if(!$this->data) return [GameCategory::$habanero];

                $categories = [];

                array_push($categories, GameCategory::$habanero);
                if($this->data['Type'] === 'Video Slots') array_push($categories, GameCategory::$slots);
                else if($this->data['Type'] === 'Casino Poker') array_push($categories, GameCategory::$table);
                else if($this->data['Type'] === 'Video Poker') array_push($categories, GameCategory::$table);
                else if($this->data['Type'] === 'Baccarat') array_push($categories, GameCategory::$table);

                return array_merge($categories);
            }
        };
    }

    function processCallback(Request $request): array {
        return [];
    }

    function process(Data $data) {
        $gameId = uniqid();

        $gameLink = null;
        $games = $this->sendRequest('game/feed', [], true);
        foreach($games as $game) {
            if($game['Name'] === $this->metadata()->name()) $gameLink = $game['LaunchUrl'];
        }

        return [
            'response' => [
                'id' => $gameId,
                'wager' => $data->bet(),
                'type' => 'third-party',
                'link' => $gameLink
            ]
        ];
    }

    public function createInstances(): array {
        $games = $this->sendRequest('game/feed', [], true);

        $instances = [];
        foreach($games as $game) {
            array_push($instances, new HabaneroGame(array_merge([
                'id' => $game['KeyName']
            ], $game)));
        }

        return $instances;
    }

    private function sendRequest(string $type, array $args = [], bool $cache = false): array {
        $cacheKey = 'habanero:'.$type.':'.http_build_query($args);
        if($cache && Cache::has($cacheKey)) return Cache::get($cacheKey);

        $response = json_decode(file_get_contents('https://haba88.com/'.$type.'?'.http_build_query($args)), true);

        if($cache) Cache::put($cacheKey, $response, now()->addDays(1));
        return $response;
    }

}
