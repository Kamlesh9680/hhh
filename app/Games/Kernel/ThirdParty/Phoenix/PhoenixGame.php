<?php namespace App\Games\Kernel\ThirdParty\Phoenix;

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

class PhoenixGame extends ThirdPartyGame {

    public function provider(): string {
        return "phoenix";
    }

    function metadata(): Metadata {
        return new class($this->data) extends Metadata {
            private ?array $data;

            public function __construct(?array $data) {
                $this->data = $data;
            }

            function id(): string {
                return "phoenix:".(!$this->data ? 'dummy' : $this->data['id']);
            }

            function name(): string {
                return $this->data ? $this->data['name'] : 'Dummy';
            }

            function icon(): string {
                return '/img/assets/phoenix/test.jpg';
            }

            public function category(): array {
                if(!$this->data) return [GameCategory::$phoenix];

                $categories = [];

                array_push($categories, GameCategory::$phoenix);

                return array_merge($categories);
            }
        };
    }

    function processCallback(Request $request): array {

    }

    function process(Data $data) {
        return [
            'response' => [
                'id' => '-1',
                'wager' => $data->bet(),
                'type' => 'third-party',
                'link' => 'http://localhost:8080/game/FruitSuperNova'
            ]
        ];
    }

    public function createInstances(): array {
        return [
            new PhoenixGame([
                'name' => 'Fruit Super Nova',
                'id' => 'fruitsupernova'
            ])
        ];
    }

}
