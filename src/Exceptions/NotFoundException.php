<?php

declare(strict_types=1);

namespace AbdelrahmanGado\SimpleDIContainer\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
