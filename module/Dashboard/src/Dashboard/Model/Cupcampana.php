<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dashboard\Model;
/**
 * Description of Cupcampana
 *
 * @author Administrador
 */
class Cupcampana {
    //put your code here
    public  $id_campana;
    public  $titulo;
    public  $subtitulo;
    public  $descripcion;
    public  $sobre_campana;
    public  $observaciones;
    public  $fecha_inicio;
    public  $hora_inicio;
    public  $fecha_final;
    public  $hora_final;
    public  $fecha_validez;
    public  $id_empresa;
    public  $id_user;
    public  $fecha_registro;
    public  $cantidad_cupones;
    public  $tiempo_online;
    public  $tiempo_offline;
            
    public function getId_campana() {
        return $this->id_campana;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function getSubtitulo() {
        return $this->subtitulo;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getSobre_campana() {
        return $this->sobre_campana;
    }

    public function getObservaciones() {
        return $this->observaciones;
    }

    public function getFecha_inicio() {
        return $this->fecha_inicio;
    }

    public function getHora_inicio() {
        return $this->hora_inicio;
    }

    public function getFecha_final() {
        return $this->fecha_final;
    }

    public function getHora_final() {
        return $this->hora_final;
    }

    public function getFecha_validez() {
        return $this->fecha_validez;
    }

    public function getId_empresa() {
        return $this->id_empresa;
    }

    public function getId_user() {
        return $this->id_user;
    }

    public function getFecha_registro() {
        return $this->fecha_registro;
    }

    public function getCantidad_cupones() {
        return $this->cantidad_cupones;
    }

    public function getTiempo_online() {
        return $this->tiempo_online;
    }

    public function getTiempo_offline() {
        return $this->tiempo_offline;
    }

    public function setId_campana($id_campana) {
        $this->id_campana = $id_campana;
    }

    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    public function setSubtitulo($subtitulo) {
        $this->subtitulo = $subtitulo;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setSobre_campana($sobre_campana) {
        $this->sobre_campana = $sobre_campana;
    }

    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    public function setFecha_inicio($fecha_inicio) {
        $this->fecha_inicio = $fecha_inicio;
    }

    public function setHora_inicio($hora_inicio) {
        $this->hora_inicio = $hora_inicio;
    }

    public function setFecha_final($fecha_final) {
        $this->fecha_final = $fecha_final;
    }

    public function setHora_final($hora_final) {
        $this->hora_final = $hora_final;
    }

    public function setFecha_validez($fecha_validez) {
        $this->fecha_validez = $fecha_validez;
    }

    public function setId_empresa($id_empresa) {
        $this->id_empresa = $id_empresa;
    }

    public function setId_user($id_user) {
        $this->id_user = $id_user;
    }

    public function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
    }

    public function setCantidad_cupones($cantidad_cupones) {
        $this->cantidad_cupones = $cantidad_cupones;
    }

    public function setTiempo_online($tiempo_online) {
        $this->tiempo_online = $tiempo_online;
    }

    public function setTiempo_offline($tiempo_offline) {
        $this->tiempo_offline = $tiempo_offline;
    }

    public function exchangeArray($data)
    {
        $this->id_campana = (isset($data['id_campana'])) ? $data['id_campana'] : null;
        $this->titulo = (isset($data['titulo'])) ? $data['titulo'] : null;
        $this->subtitulo = (isset($data['subtitulo'])) ? $data['subtitulo'] : null;
        $this->descripcion = (isset($data['descripcion'])) ? $data['descripcion'] : null;
        $this->sobre_campana = (isset($data['sobre_campana'])) ? $data['sobre_campana'] : null;
        $this->observaciones = (isset($data['observaciones'])) ? $data['observaciones'] : null;
        $this->fecha_inicio = (isset($data['fecha_inicio'])) ? $data['fecha_inicio'] : null;
        $this->hora_inicio = (isset($data['hora_inicio'])) ? $data['hora_inicio'] : null;
        $this->fecha_final = (isset($data['fecha_final'])) ? $data['fecha_final'] : null;
        $this->hora_final = (isset($data['hora_final'])) ? $data['hora_final'] : null;
        $this->fecha_validez = (isset($data['fecha_validez'])) ? $data['fecha_validez'] : null;
        $this->id_empresa = (isset($data['id_empresa'])) ? $data['id_empresa'] : null;
        $this->id_user = (isset($data['id_user'])) ? $data['id_user'] : null;
        $this->fecha_registro = (isset($data['fecha_registro'])) ? $data['fecha_registro'] : null;
        $this->cantidad_cupones = (isset($data['cantidad_cupones'])) ? $data['cantidad_cupones'] : null;
        $this->tiempo_online = (isset($data['tiempo_online'])) ? $data['tiempo_online'] : null;
        $this->tiempo_offline = (isset($data['tiempo_offline'])) ? $data['tiempo_offline'] : null;
        
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}
