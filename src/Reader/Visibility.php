<?php

namespace DoekeNorg\Decreator\Reader;

/**
 * Represents the visibility of a method.
 * @since $ver$
 */
enum Visibility: string
{
    case Public = 'public';
    case Protected = 'protected';
    case Private = 'private';
}