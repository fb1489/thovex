<?php declare(strict_types=1);
/**
 * @var \App\View\AppView $this
 * @var \App\Maps\MapMarkerList $mapMarkerList
 */
?>

<div id="vue-map-app"
    data-map-markers='<?= json_encode($mapMarkerList->items()) ?>'
></div>
