<?php

declare(strict_types=1);

namespace DoekeNorg\DecoratePhp\Renderer;

interface Renderer
{
    /**
     * @throws CouldNotRender
     */
    public function render(RenderRequest $request);
}
