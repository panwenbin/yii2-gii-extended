<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

$createdByColumn = $tableSchema->getColumn('created_by');
$updatedByColumn = $tableSchema->getColumn('updated_by');
$createdAtColumn = $tableSchema->getColumn('created_at');
$updatedAtColumn = $tableSchema->getColumn('updated_at');
$useBlameableBehavior = $createdByColumn || $updatedByColumn;
$useTimeStampBehavior = $createdAtColumn || $updatedAtColumn;
$useNow = ($createdAtColumn && $createdAtColumn->phpType == 'string') || ($updatedAtColumn && $updatedAtColumn->phpType == 'string');
echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
<?php if ($useBlameableBehavior): ?>
use yii\behaviors\BlameableBehavior;
<?php endif; ?>
<?php if ($useTimeStampBehavior): ?>
use yii\behaviors\TimestampBehavior;
<?php endif; ?>
<?php if ($useNow): ?>
use yii\db\Expression;
<?php endif; ?>

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
    /**
    * @inheritdoc
    */
    public function behaviors()
    {
        return [
<?php if($useBlameableBehavior):?>
            [
                'class' => BlameableBehavior::className(),
<?php if (!$createdByColumn): ?>
                'createdByAttribute' => null,
<?php endif;?>
<?php if (!$updatedByColumn): ?>
                'updatedByAttribute' => null,
<?php endif;?>
            ],
<?php endif;?>
<?php if($useTimeStampBehavior):?>
            [
                'class' => TimestampBehavior::className(),
<?php if (!$createdAtColumn): ?>
                'createdAtAttribute' => null,
<?php endif;?>
<?php if (!$updatedAtColumn): ?>
                'updatedAtAttribute' => null,
<?php endif;?>
<?php if ($useNow): ?>
                'value' => new Express('NOW()'),
<?php endif; ?>
            ],
<?php endif;?>
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
}
