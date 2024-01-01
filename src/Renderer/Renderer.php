<?php

namespace DoekeNorg\Decreator\Renderer;

use DoekeNorg\Decreator\Request;

interface Renderer
{
    /**
     * @throws CouldNotRender
     */
    public function render(Request $request);
}
