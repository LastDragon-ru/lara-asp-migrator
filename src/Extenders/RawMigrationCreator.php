<?php declare(strict_types = 1);

namespace LastDragon_ru\LaraASP\Migrator\Extenders;

use Illuminate\Database\Migrations\MigrationCreator;

use function basename;
use function dirname;

class RawMigrationCreator extends MigrationCreator {
    // <editor-fold desc="Illuminate\Database\Migrations\MigrationCreator">
    // =========================================================================
    /**
     * @inheritdoc
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function create($name, $path, $table = null, $create = false) {
        $path = parent::create($name, $path, $table, $create);
        $raws = $this->getRawFiles($path);

        foreach ($raws as $raw) {
            $this->files->put($raw, '');
        }

        return $path;
    }

    /**
     * @inheritdoc
     * @noinspection PhpMissingReturnTypeInspection
     */
    protected function getStub($table, $create) {
        $custom = $this->customStubPath.'/migration.stub';
        $path   = !$this->files->exists($custom)
            ? __DIR__.'/../../stubs/migration.stub'
            : $custom;
        $stub   = $this->files->get($path);

        return $stub;
    }

    // </editor-fold>

    // <editor-fold desc="Functions">
    // =========================================================================
    /**
     * @return array<string>
     */
    protected function getRawFiles(string $path): array {
        $dir  = dirname($path);
        $name = basename($path, '.php');

        return [
            "{$dir}/{$name}~up.sql",
            "{$dir}/{$name}~down.sql",
        ];
    }
    // </editor-fold>
}
