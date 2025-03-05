<template>
    <button :type="type" class="neon-button">
        <slot />
    </button>
</template>
<script>
export default {
    props: {
        type: {
            type: String,
            default: 'button'
        }
    },
}
</script>
<style scoped>
/* From Uiverse.io by S4tyendra */
.neon-button {
    position: relative;
    padding: 5px 10px;
    border-radius: 5px;
    border: 1px solid #B339FF;
    font-size: 14px;
    background: transparent;
    color: #B339FF;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 0 0 0 transparent;
    transition: all 0.3s ease-in-out;
}

.neon-button:hover {
    background: #B339FF20;
    box-shadow: 0 0 0 0 #B339FF80;
}

.neon-button::before {
    content: "";
    display: block;
    width: 0px;
    height: 86%;
    position: absolute;
    top: 7%;
    left: 0%;
    opacity: 0;
    background: linear-gradient(90deg, transparent, #B339FF, transparent);
    box-shadow: 0 0 50px 30px #d38cff;
    transform: skewX(-20deg);
    transition: all 0.5s ease-in-out;
}

.neon-button:hover::before {
    animation: neon-shine 0.6s 0s linear;
}

@keyframes neon-shine {
    from {
        opacity: 0;
        left: 0%;
    }

    50% {
        opacity: 1;
    }

    to {
        opacity: 0;
        left: 100%;
    }
}

.neon-button:active {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 transparent;
    transition: all 0.2s ease-in;
}
</style>