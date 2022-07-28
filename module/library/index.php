
<?php
require_once __DIR__.'/../../core/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') $value = addBook::do()->new();

$array = redact::do()->valueRequestGet();

echo "<ul>";
if (!$array or $array[0][3] != 1) view::do()->backBut();
if ($array) {
    foreach ($array as list($name, $id, $bookId, $parentId)) {
        if (!$bookId) view::do()->folderBut($name, $id);
        else view::do()->bookBut($name, $bookId);
    }
}
else echo 'Нет файлов и папок';
echo "</ul>";
view::do()->newFolder();
view::do()->loadBook();

$boo = tree::do()->treeLevel(1);
?>

<div name="container" onclick="treeHide()">
    <ol>
        <li>
            <span class="show">Библиотека<span>
                    <ol>
                        <?php echo $boo; ?>
                    </ol>
        </li>
    </ol>
</div>






