<?php
set_time_limit(0);
require_once '../app/Mage.php';
Mage::app();
?>

<table class="category-data" border="1" align="center">
<tr>
	<td><h2>Category Name</h2></td>
	<td><h2>Category Id</h2></td>
	</tr>
<?php
$category = Mage::getModel('catalog/category');
$tree = $category->getTreeModel();
$tree->load();

$ids = $tree->getCollection()->getAllIds();
$categories = array();
if ($ids)
{
    foreach ($ids as $id)
    {
        $category->load($id);
		$root = 'Root Catalog';
			$isRoot = strtolower($root);
			$categoryName = strtolower($category->getName());
			if($categoryName == $isRoot){
				continue;
			}
        $categories[$id]['name'] = $category->getName();
        $categories[$id]['path'] = $category->getPath();
    }
    foreach ($ids as $id)
    {
		
        $path = explode('/', $categories[$id]['path']);
		$len = count($path);
        $string = '';
		if($id > 2){
			foreach ($path as $k=>$pathId)
			{
				$separator = '';
				if($pathId > 2){
					if($k != $len-1){ $separator = ' || ';}
					$string.= $categories[$pathId]['name'] . $separator;
				}
				$cnt++;
			}
		?>
		<tr>
		<td><?php echo $string; ?></td>
		<td><?php echo $id; ?></td>
		</tr>
		<?php
		}
	?>
	
	<?php
    }
}
?>
</table>

