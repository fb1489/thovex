<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTitleColumnToMapMarkers extends AbstractMigration
{
    public function change(): void
    {
        $this->table('map_markers')
            ->addColumn('title', 'string', ['null' => true, 'default' => null])
            ->update();
    }
}
