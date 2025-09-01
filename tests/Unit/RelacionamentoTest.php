<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use Illuminate\Support\Facades\Config;

class RelacionamentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_livro_belongs_to_editora_and_has_authors()
    {
        
        $editora = Editora::factory()->create();

        $autores = Autor::factory()->count(3)->create();

        $livro = Livro::factory()->for($editora)->create();

        $livro->autores()->sync($autores->pluck('id'));
        
        $this->assertCount(3, $livro->autores);

        $livro->refresh();

        $this->assertEquals($editora->id, $livro->editora->id);

        $this->assertCount(3, $livro->autores);

        foreach ($autores as $autor) {
            $this->assertTrue($livro->autores->contains($autor));
        }
    }
}
