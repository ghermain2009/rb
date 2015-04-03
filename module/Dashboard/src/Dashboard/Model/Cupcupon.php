<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dashboard\Model;
/**
 * Description of Cupcupon
 *
 * @author Administrador
 */
class Cupcupon {
    //put your code here
    public $id_cupon;
    public $codigo_cupon;
    public $email_cliente;
    public $id_campana;
    public $id_opcion;
    public $cantidad;
    public $precio_unitario;
    public $precio_total;
    public $id_tarjeta;
    public $id_estado_compra;
    
    function getId_cupon() {
        return $this->id_cupon;
    }

    function getCodigo_cupon() {
        return $this->codigo_cupon;
    }

    function getEmail_cliente() {
        return $this->email_cliente;
    }

    function getId_campana() {
        return $this->id_campana;
    }
    
    function getId_campana_opcion() {
        return $this->id_campana_opcion;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function getPrecio_unitario() {
        return $this->precio_unitario;
    }

    function getPrecio_total() {
        return $this->precio_total;
    }

    function getId_tarjeta() {
        return $this->id_tarjeta;
    }

    function getId_estado_compra() {
        return $this->id_estado_compra;
    }

    function setId_cupon($id_cupon) {
        $this->id_cupon = $id_cupon;
    }

    function setCodigo_cupon($codigo_cupon) {
        $this->codigo_cupon = $codigo_cupon;
    }

    function setEmail_cliente($email_cliente) {
        $this->email_cliente = $email_cliente;
    }

    function setId_campana($id_campana) {
        $this->id_campana = $id_campana;
    }
    
    function setId_campana_opcion($id_campana_opcion) {
        $this->id_campana_opcion = $id_campana_opcion;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    function setPrecio_unitario($precio_unitario) {
        $this->precio_unitario = $precio_unitario;
    }

    function setPrecio_total($precio_total) {
        $this->precio_total = $precio_total;
    }

    function setId_tarjeta($id_tarjeta) {
        $this->id_tarjeta = $id_tarjeta;
    }

    function setId_estado_compra($id_estado_compra) {
        $this->id_estado_compra = $id_estado_compra;
    }

    public function exchangeArray($data)
    {
        $this->id_cupon = (isset($data['id_cupon'])) ? $data['id_cupon'] : null;
        $this->codigo_cupon = (isset($data['codigo_cupon'])) ? $data['codigo_cupon'] : null;
        $this->email_cliente = (isset($data['email_cliente'])) ? $data['email_cliente'] : null;
        $this->id_campana = (isset($data['id_campana'])) ? $data['id_campana'] : null;
        $this->id_campana_opcion = (isset($data['id_campana_opcion'])) ? $data['id_campana_opcion'] : null;
        $this->cantidad = (isset($data['cantidad'])) ? $data['cantidad'] : null;
        $this->precio_unitario = (isset($data['precio_unitario'])) ? $data['precio_unitario'] : null;
        $this->precio_total = (isset($data['precio_total'])) ? $data['precio_total'] : null;
        $this->id_tarjeta = (isset($data['id_tarjeta'])) ? $data['id_tarjeta'] : null;
        $this->id_estado_compra = (isset($data['id_estado_compra'])) ? $data['id_estado_compra'] : null;
  
    }
 
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
}
