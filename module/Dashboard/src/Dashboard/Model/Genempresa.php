<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dashboard\Model;
/**
 * Description of Genempresa
 *
 * @author gtapia
 */
class Genempresa {
    //put your code here
    public $id_empresa;
    public $razon_social;
    public $registro_contribuyente;
    public $direccion_facturacion;
    public $direcciion_comercial;
    public $telefono;
    public $horario;
    public $web_site;
    public $ubicacion_gps;
    public $numero_cuenta;
    public $descripcion;
    public $tipo_documento_representante;
    public $documento_representante;
    public $nombre_representante;
    public $rubro;
    public $id_operador;
    
    public function getId_empresa() {
        return $this->id_empresa;
    }

    public function getRazon_social() {
        return $this->razon_social;
    }

    public function getRegistro_contribuyente() {
        return $this->registro_contribuyente;
    }

    public function getDireccion_facturacion() {
        return $this->direccion_facturacion;
    }

    public function getDirecciion_comercial() {
        return $this->direcciion_comercial;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getHorario() {
        return $this->horario;
    }

    public function getWeb_site() {
        return $this->web_site;
    }

    public function getUbicacion_gps() {
        return $this->ubicacion_gps;
    }

    public function getNumero_cuenta() {
        return $this->numero_cuenta;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getTipo_documento_representante() {
        return $this->tipo_documento_representante;
    }

    public function getDocumento_representante() {
        return $this->documento_representante;
    }

    public function getNombre_representante() {
        return $this->nombre_representante;
    }
    
    public function getRubro() {
        return $this->rubro;
    }

    public function getId_operador() {
        return $this->id_operador;
    }

    public function setId_empresa($id_empresa) {
        $this->id_empresa = $id_empresa;
    }

    public function setRazon_social($razon_social) {
        $this->razon_social = $razon_social;
    }

    public function setRegistro_contribuyente($registro_contribuyente) {
        $this->registro_contribuyente = $registro_contribuyente;
    }

    public function setDireccion_facturacion($direccion_facturacion) {
        $this->direccion_facturacion = $direccion_facturacion;
    }

    public function setDirecciion_comercial($direcciion_comercial) {
        $this->direcciion_comercial = $direcciion_comercial;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setHorario($horario) {
        $this->horario = $horario;
    }

    public function setWeb_site($web_site) {
        $this->web_site = $web_site;
    }

    public function setUbicacion_gps($ubicacion_gps) {
        $this->ubicacion_gps = $ubicacion_gps;
    }

    public function setNumero_cuenta($numero_cuenta) {
        $this->numero_cuenta = $numero_cuenta;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setTipo_documento_representante($tipo_documento_representante) {
        $this->tipo_documento_representante = $tipo_documento_representante;
    }

    public function setDocumento_representante($documento_representante) {
        $this->documento_representante = $documento_representante;
    }

    public function setNombre_representante($nombre_representante) {
        $this->nombre_representante = $nombre_representante;
    }
    
    public function setRubro($rubro) {
        $this->rubro = $rubro;
    }

    public function setId_operador($id_operador) {
        $this->id_operador = $id_operador;
    }
    
    public function exchangeArray($data)
    {
        $this->id_empresa = (isset($data['id_empresa'])) ? $data['id_empresa'] : null;
        $this->razon_social = (isset($data['razon_social'])) ? $data['razon_social'] : null;
        $this->registro_contribuyente = (isset($data['registro_contribuyente'])) ? $data['registro_contribuyente'] : null;
        $this->direccion_facturacion = (isset($data['direccion_facturacion'])) ? $data['direccion_facturacion'] : null;
        $this->direccion_comercial = (isset($data['direccion_comercial'])) ? $data['direccion_comercial'] : null;
        $this->telefono = (isset($data['telefono'])) ? $data['telefono'] : null;
        $this->horario = (isset($data['horario'])) ? $data['horario'] : null;
        $this->web_site = (isset($data['web_site'])) ? $data['web_site'] : null;
        $this->ubicacion_gps = (isset($data['ubicacion_gps'])) ? $data['ubicacion_gps'] : null;
        $this->numero_cuenta = (isset($data['numero_cuenta'])) ? $data['numero_cuenta'] : null;
        $this->descripcion = (isset($data['descripcion'])) ? $data['descripcion'] : null;
        $this->tipo_documento_representante = (isset($data['tipo_documento_representante'])) ? $data['tipo_documento_representante'] : null;
        $this->documento_representante = (isset($data['documento_representante'])) ? $data['documento_representante'] : null;
        $this->nombre_representante = (isset($data['nombre_representante'])) ? $data['nombre_representante'] : null;
        $this->rubro = (isset($data['rubro'])) ? $data['rubro'] : null;
        $this->id_operador = (isset($data['id_operador'])) ? $data['id_operador'] : null;
    }
 
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }


}
