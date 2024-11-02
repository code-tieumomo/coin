<template>
  <Teleport to="body">
    <div>
      <p>I am info modal</p>
      <button @click="$modalRouter.close()">Close</button>
    </div>
  </Teleport>
</template>
