<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Install schema
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /*
         * Drop tables if exists
         */
        $installer->getConnection()->dropTable($installer->getTable('forix_bannerslider_slider'));
        $installer->getConnection()->dropTable($installer->getTable('forix_bannerslider_banner'));
        $installer->getConnection()->dropTable($installer->getTable('forix_bannerslider_report'));
        $installer->getConnection()->dropTable($installer->getTable('forix_bannerslider_value'));

        /*
         * Create table forix_bannerslider_slider
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('forix_bannerslider_slider')
        )->addColumn(
            'slider_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Slider ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Slider title'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Slider position'
        )->addColumn(
            'show_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => true, 'default' => '1'],
            'Show Title'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Slider status'
        )->addColumn(
            'sort_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['nullable' => true],
            'Sort type'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Slider description'
        )->addColumn(
            'category_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Slider category ids'
        )->addColumn(
            'style_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Slider style content'
        )->addColumn(
            'custom_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Slider custom code'
        )->addColumn(
            'style_slide',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Slider style'
        )->addColumn(
            'width',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            10,
            ['nullable' => true],
            'Slider width'
        )->addColumn(
            'height',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            10,
            ['nullable' => true],
            'Slider height'
        )->addColumn(
            'note_color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            40,
            ['nullable' => true],
            'Slider note color'
        )->addColumn(
            'animationB',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Slider animationB'
        )->addColumn(
            'caption',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Slider caption'
        )->addColumn(
            'position_note',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => true, 'default' => '1'],
            'Slider position note'
        )->addColumn(
            'slider_speed',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            10,
            ['nullable' => true],
            'Slider speed'
        )->addColumn(
            'url_view',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Slider url view'
        )->addColumn(
            'min_item',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Slider min item'
        )->addColumn(
            'max_item',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Slider max item'
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_slider', ['slider_id']),
            ['slider_id']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_slider', ['position']),
            ['position']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_slider', ['style_content']),
            ['style_content']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_slider', ['style_slide']),
            ['style_slide']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_slider', ['status']),
            ['status']
        );
        $installer->getConnection()->createTable($table);
        /*
         * End create table forix_bannerslider_slider
         */

        /*
         * Create table forix_bannerslider_banner
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('forix_bannerslider_banner')
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Image ID'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Image name'
        )->addColumn(
            'order_banner',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['nullable' => true, 'default' => 0],
            'Image order'
        )->addColumn(
            'slider_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['nullable' => true],
            'Slider Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Image status'
        )->addColumn(
            'click_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Banner click url'
        )->addColumn(
            'button_text',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Button (link) text'
        )->addColumn(
            'imptotal',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['nullable' => true, 'default' => '0'],
            'Banner imptotal'
        )->addColumn(
            'clicktotal',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['nullable' => true, 'default' => '0'],
            'Banner click total'
        )->addColumn(
            'target',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['nullable' => true, 'default' => '0'],
            'Image target'
        )->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Image'
        )->addColumn(
            'image_alt',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Image alt'
        )->addColumn(
            'align_text',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Image texts placement'
        )->addColumn(
            'width',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            10,
            ['nullable' => true],
            'Slider width'
        )->addColumn(
            'height',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            10,
            ['nullable' => true],
            'Slider height'
        )->addColumn(
            'style_slide',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Banner style'
        )->addColumn(
            'width',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            10,
            ['nullable' => true],
            'Banner width'
        )->addColumn(
            'height',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            10,
            ['nullable' => true],
            'Image height'
        )->addColumn(
            'start_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Image starting time'
        )->addColumn(
            'end_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Image ending time'
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_banner', ['banner_id']),
            ['banner_id']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_banner', ['slider_id']),
            ['slider_id']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_banner', ['status']),
            ['status']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_banner', ['start_time']),
            ['start_time']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_banner', ['end_time']),
            ['end_time']
        );
        $installer->getConnection()->createTable($table);
        /*
         * End create table forix_bannerslider_banner
         */

        /*
         * Create table forix_bannerslider_value
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('forix_bannerslider_value')
        )->addColumn(
            'value_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Value ID'
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Image ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store view ID'
        )->addColumn(
            'attribute_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            63,
            ['nullable' => false, 'default' => ''],
            'Attribute code'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Value'
        )->addIndex(
            $installer->getIdxName(
                'forix_bannerslider_value',
                ['banner_id', 'store_id', 'attribute_code'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['banner_id', 'store_id', 'attribute_code'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_value', ['banner_id']),
            ['banner_id']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_value', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName(
                'forix_bannerslider_value',
                'banner_id',
                'forix_bannerslider_banner',
                'banner_id'
            ),
            'banner_id',
            $installer->getTable('forix_bannerslider_banner'),
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'forix_bannerslider_value',
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);
        /*
         * End create table forix_bannerslider_value
         */

        /*
         * Create table forix_bannerslider_report
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('forix_bannerslider_report')
        )->addColumn(
            'report_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Report ID'
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Image ID'
        )->addColumn(
            'slider_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Slider ID'
        )->addColumn(
            'impmode',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Impressions mode'
        )->addColumn(
            'clicks',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Image Clicks'
        )->addColumn(
            'date_click',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Image click date time'
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_report', ['banner_id']),
            ['banner_id']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_report', ['slider_id']),
            ['slider_id']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_report', ['impmode']),
            ['impmode']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_report', ['clicks']),
            ['clicks']
        )->addIndex(
            $installer->getIdxName('forix_bannerslider_report', ['date_click']),
            ['date_click']
        );
        $installer->getConnection()->createTable($table);
        /*
         * End create table forix_bannerslider_report
         */

        $installer->endSetup();
    }
}
