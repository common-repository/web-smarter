<div id="app">
  <div class="">
    <component :is="currentView" @update="changeComponent" :currentuser="currentUser" :config="config" transition="fade" transition-mode="out-in"></component>
  </div>
</div>
<?php 
/* Include all common components file */
foreach (glob(WSP_VUE."components/common/*.php") as $filename)
{
	include_once($filename);
	echo nl2br(file_get_contents($filename));
}
/* Include all components file */
foreach (glob(WSP_VUE."components/*.php") as $filename)
{
	include_once($filename);
	echo nl2br(file_get_contents($filename));
}
?>