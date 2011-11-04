<?php
/**
 * Класс для построения страниц.
 * Класс Application не подходит для этй цели,
 * поскуольку если запускать код в режиме TrueFastCGI (phpDaemon)
 * то экземпляр класса Appklication будет общим для все хитов
 * @author pr0n1x
 */
class Page extends AbstractLogger implements PageInterface {
    
}
?>
