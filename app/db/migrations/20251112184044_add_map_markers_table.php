<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddMapMarkersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('map_markers')
            ->addColumn('coordinates', 'point', ['null' => false])
            ->create();
    }
}
