<?php

use think\admin\extend\PhinxExtend;
use think\migration\Migrator;

class InstallTelegram extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->_create_plugin_telegram_account();
        $this->_create_plugin_telegram_channel();
        $this->_create_plugin_telegram_channel_source();
        $this->_create_plugin_telegram_channel_content();
        $this->_create_plugin_telegram_base_keyword();
        $this->_create_plugin_telegram_channel_release();
        $this->_create_plugin_telegram_release_type();
    }

    /**
     * Telegram账号
     * @class PluginTelegramAccount
     * @table plugin_telegram_account
     * @return void
     */
    private function _create_plugin_telegram_account()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_account', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Telegram账号',
        ]);

        PhinxExtend::upgrade($table, [
            ['account_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => 'Telegram账号ID']],
            ['title', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '账号标题']],
            ['api_id', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => 'API ID']],
            ['api_hash', 'string', ['limit' => 32, 'default' => NULL, 'null' => true, 'comment' => 'API Hash']],
            ['first_name', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '账号的名字']],
            ['last_name', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '账号的姓氏']],
            ['username', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '账号名']],
            ['phone_number', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '账号的手机号']],
            ['photo', 'string', ['limit' => 32, 'default' => NULL, 'null' => true, 'comment' => '账号的头像']],
            ['access_hash', 'string', ['limit' => 32, 'default' => NULL, 'null' => true, 'comment' => '账号的访问哈希']],
            ['user_status', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '账号的状态（如 online, last_seen)']],
            ['remark', 'string', ['limit' => 500, 'default' => NULL, 'null' => true, 'comment' => '备注(内部使用)']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '账号状态(0拉黑,1正常)']],
            ['deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
            ['update_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间']],
        ], [
            'account_id', 'api_id', 'username' , 'deleted', 'phone_number' , 'create_at',
        ], true);
    }

    /**
     * 经营频道
     * @class PluginTelegramChannel
     * @table plugin_telegram_channel
     * @return void
     */
    private function _create_plugin_telegram_channel()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_channel', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '经营频道',
        ]);

        PhinxExtend::upgrade($table, [
            ['account_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '管理账号']],
            ['channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '频道ID']],
            ['channel_title', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '频道标题']],
            ['channel_name', 'string', ['limit' => 32, 'default' => NULL, 'null' => true, 'comment' => '频道名']],
            ['channel_link', 'string', ['limit' => 64, 'default' => NULL, 'null' => true, 'comment' => '频道链接']],
            ['channel_type', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '频道类型']],
            ['remark', 'string', ['limit' => 500, 'default' => NULL, 'null' => true, 'comment' => '备注(内部使用)']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '频道状态(0拉黑,1正常)']],
            ['deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
            ['update_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间']],
        ], [
            'channel_id', 'channel_name' ,'deleted', 'create_at',
        ], true);
    }


    /**
     * 目标频道
     * @class PluginTelegramChannelSource
     * @table plugin_telegram_channel_source
     * @return void
     */
    private function _create_plugin_telegram_channel_source()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_channel_source', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '目标频道',
        ]);

        PhinxExtend::upgrade($table, [
            ['account_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '管理账号']],
            ['channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '频道ID']],
            ['release_channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '所属频道ID']],
            ['channel_title', 'string', ['limit' => 16, 'default' => NULL, 'null' => true, 'comment' => '频道标题']],
            ['channel_name', 'string', ['limit' => 32, 'default' => NULL, 'null' => true, 'comment' => '频道名']],
            ['channel_link', 'string', ['limit' => 64, 'default' => NULL, 'null' => true, 'comment' => '频道链接']],
            ['channel_type', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '内容类型(0最新内容,1历史内容)']],
            ['limit_number', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '限制数量']],
            ['last_message_id', 'integer', ['default' => 0, 'null' => true, 'comment' => '历史内容ID']],
            ['remark', 'string', ['limit' => 500, 'default' => NULL, 'null' => true, 'comment' => '备注(内部使用)']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '频道状态(0拉黑,1正常)']],
            ['deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
            ['update_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间']],
        ], [
            'account_id', 'channel_id','release_channel_id', 'channel_name' ,'deleted', 'create_at',
        ], true);
    }

    /**
     * 目标频道内容
     * @class PluginTelegramChannelContent
     * @table plugin_telegram_channel_content
     * @return void
     */
    private function _create_plugin_telegram_channel_content()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_channel_content', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '目标频道内容',
        ]);
        PhinxExtend::upgrade($table, [
            ['channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '来源频道ID']],
            ['account_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '采集账号ID']],
            ['new_message_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '频道消息ID']],
            ['message_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '来源频道消息ID']],
            ['grouped_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '组合消息ID']],
            ['caption', 'text', ['default' => NULL, 'null' => true, 'comment' => '消息内容']],
            ['type', 'string', ['limit' => 20, 'default' => NULL, 'null' => true, 'comment' => '媒体类型']],
            ['media', 'string', ['limit' => 255, 'default' => null, 'null' => true, 'comment' => '媒体文件ID']],
            ['date', 'integer', ['limit' => 32, 'default' => 0, 'null' => true, 'comment' => '发送时间']],
            ['cover', 'longtext', ['default' => NULL, 'null' => true, 'comment' => '媒体信息,base64信息']],
            ['forward', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '发布状态(0未转发,1已转发)']],
            ['is_replies', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '评论状态(0无评论,1有评论)']],
            ['replies_status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '评论采集(0未采集,1已采集)']],
            ['replies', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '评论数量']],
            ['cate', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '类型(0内容,1评论)']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '收录状态(0未收录,1已收录)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'channel_id','account_id','grouped_id','message_id','date','forward','status','cate','is_replies','create_at',
        ], true);
    }

    /**
     * 敏感词管理
     * @class PluginTelegramBaseKeyword
     * @table plugin_telegram_base_keyword
     * @return void
     */
    private function _create_plugin_telegram_base_keyword()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_base_keyword', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '敏感词管理',
        ]);
        PhinxExtend::upgrade($table, [
            ['name', 'string', ['limit' => 16, 'default' => null, 'null' => true, 'comment' => '敏感词']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '状态(0拉黑,1正常)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'create_at'
        ], true);
    }

    /**
     * 频道发布内容管理
     * @class PluginTelegramChannelRelease
     * @table plugin_telegram_channel_release
     * @return void
     */
    private function _create_plugin_telegram_channel_release()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_channel_release', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '频道发布内容管理',
        ]);
        PhinxExtend::upgrade($table, [
            ['channel_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '发布频道ID']],
            ['type_id', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '分类ID']],
            ['caption', 'string', ['limit' => 500, 'default' => null, 'null' => true, 'comment' => '内容标题']],
            ['media', 'longtext', ['default' => null, 'null' => true, 'comment' => '内容JSON']],
            ['bot_token', 'string', ['limit' => 64, 'default' => NULL, 'null' => true, 'comment' => 'API TOKE']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '状态(0未发布,1已发布)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
            ['update_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间']],
        ], [
            'channel_id','type_id','status','create_at','update_at'
        ], true);
    }

    /**
     * 内容分类
     * @class PluginTelegramReleaseType
     * @table plugin_telegram_release_type
     * @return void
     */
    private function _create_plugin_telegram_release_type()
    {
        // 创建数据表对象
        $table = $this->table('plugin_telegram_release_type', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '内容分类',
        ]);
        PhinxExtend::upgrade($table, [
            ['name', 'string', ['limit' => 16, 'default' => null, 'null' => true, 'comment' => '分类名称']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '状态(0拉黑,1正常)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'create_at'
        ], true);
    }

}
