<?php declare(strict_types = 1);

namespace LastDragon_ru\LaraASP\Migrator\Migrations;

use Illuminate\Database\Connection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Migrations\Migrator;
use LastDragon_ru\LaraASP\Migrator\Extenders\SmartMigrator;
use LastDragon_ru\LaraASP\Migrator\Traits\SqlHelper;
use ReflectionClass;
use RuntimeException;

abstract class SqlMigration extends Migration {
    use SqlHelper;

    protected ?Migrator $migrator       = null;
    protected ?string   $up             = null;
    protected ?string   $down           = null;
    protected bool      $withDriverName = false;

    public function __construct() {
        // empty
    }

    // <editor-fold desc="API">
    // =========================================================================
    public function onConnection(string $connection): static {
        $this->connection = $connection;

        return $this;
    }

    public function withinTransaction(bool $withinTransaction): static {
        $this->withinTransaction = $withinTransaction;

        return $this;
    }

    public function withDriverName(bool $withDriverName): static {
        $this->withDriverName = $withDriverName;

        return $this;
    }

    public function upFrom(?string $path): static {
        $this->up = $path;

        return $this;
    }

    public function downFrom(?string $path): static {
        $this->down = $path;

        return $this;
    }
    // </editor-fold>

    // <editor-fold desc="Getters/Setters">
    // =========================================================================
    protected function getConnectionInstance(): Connection {
        if ($this->migrator === null) {
            throw new RuntimeException('Unknown connection.');
        }

        return $this->migrator->resolveConnection((string) $this->getConnection());
    }

    /**
     * @return list<string>
     */
    protected function getType(string $type): array {
        $types = [];

        if ($this->withDriverName) {
            $types[] = $this->getConnectionInstance()->getDriverName();
        }

        $types[] = $type;

        return $types;
    }
    // </editor-fold>

    // <editor-fold desc="Factory">
    // =========================================================================
    /**
     * @internal
     */
    public function __invoke(SmartMigrator $migrator): static {
        // Dependencies
        $this->migrator = $migrator;

        // Defaults
        $file = (new ReflectionClass($this))->getFileName();

        if ($file) {
            $this->upFrom($file);
            $this->downFrom($file);
        }

        // Return
        return $this;
    }
    // </editor-fold>

    // <editor-fold desc="Migration">
    // =========================================================================
    public function up(): void {
        if (isset($this->up)) {
            $this->runSqlFile(
                $this->getConnectionInstance(),
                $this->getSqlPath($this->up, ...$this->getType('up')),
            );
        }
    }

    public function down(): void {
        if (isset($this->down)) {
            $this->runSqlFile(
                $this->getConnectionInstance(),
                $this->getSqlPath($this->down, ...$this->getType('down')),
            );
        }
    }
    // </editor-fold>
}
