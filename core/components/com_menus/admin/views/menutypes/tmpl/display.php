<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>
<script type="text/javascript">
    setmenutype = function(type)
    {
        window.parent.Hubzero.submitbutton('items.setType', type);
        window.parent.$.fancybox.close();
    }
</script>

<h2 class="modal-title"><?php echo Lang::txt('COM_MENUS_TYPE_CHOOSE'); ?></h2>
<ul class="menu_types">
    <?php foreach ($this->types as $name => $list) : ?>
        <li>
            <dl class="menu_type">
                <dt><?php echo Lang::txt($name);?></dt>
                <dd><ul>
                        <?php foreach ($list as $item) : ?>
                            <?php
                            $itemData = array(
                            'id' => $this->recordId,
                            'title' => $item->title,
                            'request' => $item->request
                            );
                            $encoded = base64_encode(json_encode($itemData));
                            $descTitle = Lang::txt($item->description);
                            ?>
                        <li>
                            <a class="choose_type" href="#"
                                title="<?php echo $descTitle; ?>"
                                onclick="javascript:setmenutype('<?php echo $encoded; ?>')">
                                <?php echo Lang::txt($item->title);?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </dd>
            </dl>
        </li>
    <?php endforeach; ?>
    <li>
        <dl class="menu_type">
            <dt><?php echo Lang::txt('COM_MENUS_TYPE_SYSTEM'); ?></dt>
            <dd>
                <ul>
                    <li>
                        <?php
                        $urlData = base64_encode(json_encode(
                            array('id' => $this->recordId, 'title' => 'url')
                        ));
                        $urlDesc = Lang::txt('COM_MENUS_TYPE_EXTERNAL_URL_DESC');
                        ?>
                        <a class="choose_type" href="#"
                            title="<?php echo $urlDesc; ?>"
                            onclick="javascript:setmenutype('<?php echo $urlData; ?>')">
                            <?php echo Lang::txt('COM_MENUS_TYPE_EXTERNAL_URL'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $aliasData = base64_encode(json_encode(
                            array('id' => $this->recordId, 'title' => 'alias')
                        ));
                        $aliasDesc = Lang::txt('COM_MENUS_TYPE_ALIAS_DESC');
                        ?>
                        <a class="choose_type" href="#"
                            title="<?php echo $aliasDesc; ?>"
                            onclick="javascript:setmenutype('<?php echo $aliasData; ?>')">
                            <?php echo Lang::txt('COM_MENUS_TYPE_ALIAS'); ?>
                        </a>
                    </li>
                    <li>
                        <?php
                        $sepData = base64_encode(json_encode(
                            array('id' => $this->recordId, 'title' => 'separator')
                        ));
                        $sepDesc = Lang::txt('COM_MENUS_TYPE_SEPARATOR_DESC');
                        ?>
                        <a class="choose_type" href="#"
                            title="<?php echo $sepDesc; ?>"
                            onclick="javascript:setmenutype('<?php echo $sepData; ?>')">
                            <?php echo Lang::txt('COM_MENUS_TYPE_SEPARATOR'); ?>
                        </a>
                    </li>
                </ul>
            </dd>
        </dl>
    </li>
</ul>
