<?php

// src/Matrix/MatrixUserBundle/Userbundle.php
namespace Matrix\MatrixUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MatrixUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
?>