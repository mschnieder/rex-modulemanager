<?php

/** @var rex_addon $this */

$moduleskey = rex_request('modulkey', 'string');

$modules = [];

$message = rex_api_function::getMessage();

try {
	$modules = rex_install_moduls::getUpdateModuls();
} catch (rex_functional_exception $e) {
	$message .= rex_view::warning($e->getMessage());
	$moduleskey = '';
}

if ($moduleskey && isset($modules[$moduleskey])) {
	$module = $modules[$moduleskey];

	$panel = '
        <table class="table">
            <tbody>
            <tr>
                <th class="rex-table-width-5">' . $this->i18n('name') . '</th>
                <td data-title="' . $this->i18n('name') . '">' . htmlspecialchars($module['name']) . '</td>
            </tr>
            <tr>
                <th>' . $this->i18n('author') . '</th>
                <td data-title="' . $this->i18n('author') . '">' . htmlspecialchars($module['author']) . '</td>
            </tr>
            <tr>
                <th>' . $this->i18n('shortdescription') . '</th>
                <td data-title="' . $this->i18n('shortdescription') . '">' . nl2br(htmlspecialchars($module['shortdescription'])) . '</td>
            </tr>
            <tr>
                <th>' . $this->i18n('description') . '</th>
                <td data-title="' . $this->i18n('description') . '">' . nl2br(htmlspecialchars($module['description'])) . '</td>
            </tr>
            </tbody>
        </table>';

	$fragment = new rex_fragment();
	$fragment->setVar('title', '<b>' . $moduleskey . '</b> ' . $this->i18n('information'), false);
	$fragment->setVar('content', $panel, false);
	$content = $fragment->parse('core/page/section.php');

	$panel = '
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th class="rex-table-icon"></th>
                <th class="rex-table-width-4">' . $this->i18n('version') . '</th>
                <th>' . $this->i18n('description') . '</th>
                <th class="rex-table-action"></th>
            </tr>
            </thead>
            <tbody>';

	foreach ($module['files'] as $fileId => $file) {
		$panel .= '
            <tr>
                <td class="rex-table-icon"><i class="rex-icon rex-icon-package"></i></td>
                <td data-title="' . $this->i18n('version') . '">' . htmlspecialchars($file['version']) . '</td>
                <td data-title="' . $this->i18n('description') . '">' . nl2br(htmlspecialchars($file['description'])) . '</td>
                <td class="rex-table-action"><a href="' . rex_url::currentBackendPage(['modulkey' => $moduleskey, 'file' => $fileId] + rex_api_install_module_update::getUrlParams()) . '" data-pjax="false">' . $this->i18n('update') . '</a></td>
            </tr>';
	}

	$panel .= '</tbody></table>';

	$fragment = new rex_fragment();
	$fragment->setVar('title', $this->i18n('files'), false);
	$fragment->setVar('content', $panel, false);
	$content .= $fragment->parse('core/page/section.php');
} else {
	$panel = '
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th class="rex-table-icon"><a href="' . rex_url::currentBackendPage(['func' => 'reload']) . '" title="' . $this->i18n('reload') . '"><i class="rex-icon rex-icon-refresh"></i></a></th>
                <th>' . $this->i18n('key') . '</th>
                <th>' . $this->i18n('name') . '</th>
                <th>' . $this->i18n('existing_version') . '</th>
                <th>' . $this->i18n('available_versions') . '</th>
            </tr>
            </thead>
            <tbody>';

	foreach ($modules as $key => $module) {
		$availableVersions = [];
		foreach ($module['files'] as $file) {
			$availableVersions[] = $file['version'];
		}
		$url = rex_url::currentBackendPage(['modulkey' => $key]);

		$panel .= '
            <tr>
                <td class="rex-table-icon"><a href="' . $url . '"><i class="rex-icon rex-icon-package"></i></a></td>
                <td data-title="' . $this->i18n('key') . '"><a href="' . $url . '">' . htmlspecialchars($key) . '</a></td>
                <td data-title="' . $this->i18n('name') . '">' . htmlspecialchars($module['name']) . '</td>
                <td data-title="' . $this->i18n('existing_version') . '">' . htmlspecialchars(rex_addon::get($key)->getVersion()) . '</td>
                <td data-title="' . $this->i18n('available_versions') . '">' . htmlspecialchars(implode(', ', $availableVersions)) . '</td>
            </tr>';
	}

	$panel .= '</tbody></table>';

	$fragment = new rex_fragment();
	$fragment->setVar('title', $this->i18n('available_updates', !empty($coreVersions) + count($modules)), false);
	$fragment->setVar('content', $panel, false);
	$content = $fragment->parse('core/page/section.php');
}

echo $message;
echo $content;
