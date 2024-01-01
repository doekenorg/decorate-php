<?php

namespace DoekeNorg\DecoratePhp\Renderer;

interface Renderer
{
    /**
     * @throws CouldNotRender
     */
    public function render(RenderRequest $request);
}
