<h2 class="nav-tab-wrapper">
    <?php foreach ($this->getTabs() as $key => $label) : ?>
        <?php $active = ($key == $this->getCurrentTab() ? ' nav-tab-active' : ''); ?>
        <a class="nav-tab<?php echo $active; ?>"
           href="<?php echo admin_url(self::PARENT_MENU_SLUG . '?page=' . self::MENU_SLUG . '&tab=' . $key); ?>"><?php echo $label; ?></a>
    <?php endforeach; ?>
    <?php foreach ($this->getTabs() as $key => $label) : ?>
        <?php $active = ($key == $this->getCurrentTab() ? ' nav-tab-active' : ''); ?>
        <?php if ($active && $this->getCurrentTab() == 'info') : ?>
            <h3 class="nav-tab-wrapper">
                <?php foreach ($sub_tabs as $sub_key => $sub_label) : ?>
                    <?php $active = ($sub_key == $this->getCurrentSection() ? ' nav-tab-active' : ''); ?>
                    <a class="nav-tab nav-tab-small<?php echo $active; ?>"
                       href="<?php echo admin_url(self::PARENT_MENU_SLUG . '?page=' . self::MENU_SLUG . '&tab=' . $key . '&section=' . $sub_key); ?>"><?php echo $sub_label; ?></a>
                <?php endforeach; ?>
            </h3>
        <?php endif; ?>
    <?php endforeach; ?>
</h2>
