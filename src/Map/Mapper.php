<?php

namespace Jad\Map;

interface Mapper
{
    /**
     * @param $type
     * @return mixed
     */
    public function getEntityMapItem($type);
}