<?php

namespace common\tests\unit\models;

use Codeception\Test\Unit;
use common\models\ItemDespensa;

class ItemDespensaTest extends Unit
{
    public function testNomeObrigatorio(): void
    {
        // Criar um item sem nome.
        $item = new ItemDespensa();
        $item->nome = '';

        // Validar apenas o campo nome.
        $this->assertFalse($item->validate(['nome']));

        // Confirmar que existe um erro nesse campo.
        $this->assertTrue($item->hasErrors('nome'));
    }
}