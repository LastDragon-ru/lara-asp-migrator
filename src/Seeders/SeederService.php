<?php declare(strict_types = 1);

namespace LastDragon_ru\LaraASP\Migrator\Seeders;

use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;

use function array_column;
use function in_array;
use function is_string;

class SeederService {
    public function __construct() {
        // empty
    }

    // <editor-fold desc="API">
    // =========================================================================
    public function isSeeded(): bool {
        $seeded  = false;
        $tables  = array_column($this->getConnection()->getSchemaBuilder()->getTables(), 'name');
        $skipped = [
            Container::getInstance()->make(Repository::class)->get('database.migrations'),
        ];

        foreach ($tables as $table) {
            if (in_array($table, $skipped, true)) {
                continue;
            }

            if ($this->isTableSeeded($table)) {
                $seeded = true;
                break;
            }
        }

        return $seeded;
    }

    /**
     * @param class-string<Model>|Model $model
     */
    public function isModelSeeded(string|Model $model): bool {
        if (is_string($model)) {
            $model = new $model();
        }

        return $this->isTableSeeded($model->getTable());
    }

    public function isTableSeeded(string $table): bool {
        return $this->getConnection()->table($table)->count() > 0;
    }
    // </editor-fold>

    // <editor-fold desc="Functions">
    // =========================================================================
    protected function getConnection(): Connection {
        return Container::getInstance()->make(DatabaseManager::class)->connection();
    }
    // </editor-fold>
}
