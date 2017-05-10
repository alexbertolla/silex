<?php
namespace SON\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 */
class Post {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $titulo;

    /**
     * @ORM\Column(type="text")
     */
    public $conteudo;

    public function setConteudo($conteudo) {
        $this->conteudo = $conteudo;
    }

    public function getConteudo() {
        return $this->conteudo;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    public function getTitulo() {
        return $this->titulo;
    }

}
