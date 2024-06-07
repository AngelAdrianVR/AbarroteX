<template>
    <div>
        impresion de historial de pagos
    </div>
</template>

<script>
export default {
data() {
    return {

    }
},
components:{

},
props:{

},
methods:{
    print() {
        window.print();
    }
},
mounted(){
this.print();
}
};
</script>