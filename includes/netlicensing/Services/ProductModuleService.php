<?php
/**
 * Created by PhpStorm.
 * User: Black
 * Date: 20.10.2015
 * Time: 14:34
 */

namespace NetLicensing;

class ProductModuleService extends BaseEntityService
{
    public function init()
    {
        $this->nl_connect->setResponseFormat('xml');
    }

    public static function connect(NetLicensingAPI $nl_connect)
    {
        return new ProductModuleService($nl_connect);
    }

    public function getList()
    {
        return $this->_getList($this->nl_connect);
    }

    /**
     * @param $number
     * @return ProductModule|false
     * @throws NetLicensingException
     */
    public function get($number)
    {
        return $this->_get($number, $this->nl_connect);
    }

    public function create(ProductModule $product_module)
    {
        return $this->_create($product_module, $this->nl_connect);
    }

    public function update(ProductModule $product_module)
    {
        return $this->_update($product_module, $this->nl_connect);
    }

    public function delete($number, $force_cascade = FALSE)
    {
        return $this->_delete($number, $this->nl_connect, $force_cascade);
    }

    protected function _getNewEntity()
    {
        return new ProductModule();
    }

    protected function _getServiceUrlPart()
    {
        return '/productmodule';
    }
}