<?php

namespace DoekeNorg\DecoratePhp\Renderer;

use DoekeNorg\DecoratePhp\Request;

interface Renderer
{
    /**
     * @throws CouldNotRender
     */
    public function render(Request $request);
}
