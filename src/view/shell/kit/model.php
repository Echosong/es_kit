{{?php

/**
* 自动生成数据模型
* 名称：<?=$tableComment.PHP_EOL?>
* User: echosong
* Date: <?=date('Y-m-d H:i:s').PHP_EOL?>
*/

class <?=ucfirst($table)?> extends Model
{
    //表名
    public $table_name = '<?=$tableName?>';

    //验证字段规则
    public static $rules = [
        <?=$rules?>
    ];

    //数据库字段
    public $fields = ['<?=$fields?>'];

}