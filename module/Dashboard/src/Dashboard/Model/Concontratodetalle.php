<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dashboard\Model;
/**
 * Description of Concontratodetalle
 *
 * @author gtapia
 */
class Concontratodetalle {
    //put your code here
    public $id_contrato;
    public $contador_detalle;
    public $observacion;
    public $fecha_registro;
    public $firma;
   
    public function getId_contrato() {
        return $this->id_contrato;
    }

    public function getContador_detalle() {
        return $this->contador_detalle;
    }

    public function getObservacion() {
        return $this->observacion;
    }

    public function getFecha_registro() {
        return $this->fecha_registro;
    }

    public function getFirma() {
        return $this->firma;
    }

    public function setId_contrato($id_contrato) {
        $this->id_contrato = $id_contrato;
    }

    public function setContador_detalle($contador_detalle) {
        $this->contador_detalle = $contador_detalle;
    }

    public function setObservacion($observacion) {
        $this->observacion = $observacion;
    }

    public function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
    }

    public function setFirma($firma) {
        $this->firma = $firma;
    }

    public function exchangeArray($data)
    {
        $this->id_contrato = (isset($data['id_contrato'])) ? $data['id_contrato'] : null;
        $this->contador_detalle = (isset($data['contador_detalle'])) ? $data['contador_detalle'] : null;
        $this->observacion = (isset($data['observacion'])) ? $data['observacion'] : null;
        $this->fecha_registro = (isset($data['fecha_registro'])) ? $data['fecha_registro'] : null;
        $this->firma = (isset($data['firma'])) ? $data['firma'] : null;
    }
 
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
