<?php

/*
 * This file is part of the Osynapsy package.
 *
 * (c) Pietro Celeste <p.celeste@osynapsy.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Osynapsy\Bcl4\DataGrid;

use Osynapsy\Html\Tag;

class DataGridRow extends Tag
{
    public function setMarker($latitude, $longitude, $iconColor = 'green', $icon = 'map-marker', $id = null)
    {
        $this->attribute('id', $id);
        $this->attribute('marker', json_encode([
            'coordinates' => [
                $latitude,
                $longitude
            ],
            'options' => [
                'awesomeIcon' => $icon,
                'iconColor' => $iconColor,
                'markerId' => $id
            ]
        ], JSON_HEX_APOS | JSON_HEX_QUOT));
    }
}
