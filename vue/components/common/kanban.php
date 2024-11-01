<?php /* task board Components*/ ?>
<template id="kanban-template">
  <div>
    <?php //echo file_get_contents(API_URL.'api/template?temp=kanban');?>
    <?php echo $wpsTaskFunObj->postUrlUsingCurl(API_URL.'api/template?temp=kanban');?>
  </div>
</template>