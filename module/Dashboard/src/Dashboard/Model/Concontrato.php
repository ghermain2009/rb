<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dashboard\Model;
/**
 * Description of Concontrato
 *
 * @author gtapia
 */
class Concontrato {
    //put your code here
    public $id_contrato;
    public $id_campana;
    public $id_estado;
    public $nombre_contacto;
    public $email_contacto;
    public $fecha_registro;
    
    public function getId_contrato() {
        return $this->id_contrato;
    }

    public function getId_campana() {
        return $this->id_campana;
    }

    public function getId_estado() {
        return $this->id_estado;
    }

    public function getNombre_contacto() {
        return $this->nombre_contacto;
    }

    public function getEmail_contacto() {
        return $this->email_contacto;
    }

    public function getFecha_registro() {
        return $this->fecha_registro;
    }

    public function setId_contrato($id_contrato) {
        $this->id_contrato = $id_contrato;
    }

    public function setId_campana($id_campana) {
        $this->id_campana = $id_campana;
    }

    public function setId_estado($id_estado) {
        $this->id_estado = $id_estado;
    }

    public function setNombre_contacto($nombre_contacto) {
        $this->nombre_contacto = $nombre_contacto;
    }

    public function setEmail_contacto($email_contacto) {
        $this->email_contacto = $email_contacto;
    }

    public function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
    }

    public function exchangeArray($data)
    {
        $this->id_contrato = (isset($data['id_contrato'])) ? $data['id_contrato'] : null;
        $this->id_campana = (isset($data['id_campana'])) ? $data['id_campana'] : null;
        $this->id_estado = (isset($data['id_estado'])) ? $data['id_estado'] : null;
        $this->nombre_contacto = (isset($data['nombre_contacto'])) ? $data['nombre_contacto'] : null;
        $this->email_contacto = (isset($data['email_contacto'])) ? $data['email_contacto'] : null;
        $this->fecha_registro = (isset($data['fecha_registro'])) ? $data['fecha_registro'] : null;
    }
 
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
}
