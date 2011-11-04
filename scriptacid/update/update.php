<?
if (!empty($_GET['updIDS'])) {
	if (copy('./update_clr.zip', './updates.zip')) {
		header('Content-Type: application/zip');
		header('Content-Transfer-Encoding: binary');

		$xml = simplexml_load_file("./updates.xml");
		$update = $xml->UpdateSign->attributes();



		$zip = new ZipArchive;
		$zip->open('./updates.zip');

		foreach ($xml->Files->File as $file) {
			$upd = $file->attributes();
			if (true) {
				$zip->addFile($_SERVER['DOCUMENT_ROOT'].$upd['name'], $upd['name']);
			}
		}

		$zip->close();

		echo file_get_contents('./updates.zip');
		unlink('./updates.zip');
	}
}