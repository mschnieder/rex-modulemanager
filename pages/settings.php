<?php

/** @var rex_addon $this */

$panel = '';

$configFile = $this->getDataPath('config.json');
$config = array_merge([
	'module_master_url' => null,
	'module_api_login' => null,
	'module_api_key' => null,
], rex_file::getCache($configFile));

$newConfig = rex_post('settings', [
	['module_master_url', 'string'],
	['api_login', 'string'],
	['api_key', 'string'],
], null);

if (is_array($newConfig)) {
	$config = $newConfig;
	if (rex_file::putCache($configFile, $config)) {
		echo rex_view::success($this->i18n('settings_saved'));
		rex_install_webservice::deleteCache();
	} else {
		echo rex_view::error($this->i18n('settings_error', $configFile));
	}
}

$panel .= '
            <fieldset>
                <legend>' . $this->i18n('settings_general') . '</legend>';

$formElements = [];

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$panel .= $fragment->parse('core/form/checkbox.php');

$formElements = [];

$n = [];
$n['label'] = '<label for="install-settings-api-login">' . $this->i18n('settings_master_url') . '</label>';
$n['field'] = '<div style="width: 100%;"><div style="width: 80px; float:left; text-align: center; border: 1px solid #ccc; background: #dfe3e9; padding: 6px 0px;">https://</div><input class="form-control" id="install-settings-api-login" type="text" name="settings[module_master_url]" style="width: calc(100% - 80px); float: left;" value="' . htmlspecialchars($config['module_master_url']) . '" /></div>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$panel .= $fragment->parse('core/form/form.php');

$panel .= '</fieldset>';

$formElements = [];

$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="settings[save]" value="1">' . rex_i18n::msg('form_save') . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('subpage_settings'), false);
$fragment->setVar('body', $panel, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

$content = '
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        ' . $content . '
    </form>';

echo $content;
