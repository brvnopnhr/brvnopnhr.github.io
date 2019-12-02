ALTER TABLE #__abauthor ADD lastname VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER id
ALTER TABLE #__abauthor CHANGE name name VARCHAR( 255 ) NULL
ALTER TABLE #__abbook ADD docsfolder TEXT NULL DEFAULT '' AFTER image
ALTER TABLE #__abbook ADD `pag_index` INT( 4 ) NULL AFTER `pag` 
