<?php
class Config{
    private $cfg = array(
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
        'mchId'=>'101520021587',
        'key'=>'8a4199115aa15cd81e064c796a4da1a6',
        'version'=>'1.0'
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>