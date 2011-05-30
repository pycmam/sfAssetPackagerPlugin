<?php

class sfAssetPackagerPluginConfiguration extends sfPluginConfiguration
{
    /**
     * Init
     */
    public function initialize()
    {
      $helpers = array_merge(sfConfig::get('sf_standard_helpers', array()), array('AssetPackager'));
      sfConfig::set('sf_standard_helpers', $helpers);
    }

}
