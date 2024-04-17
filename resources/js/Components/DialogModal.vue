<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div>
            <div class="float-left w-full p-4">
                <Icon @click="close" 
                    class="float-right cursor-pointer" 
                    name="close"
                    :hover="colors.green"
                    :fill="colors['spun-pearl']"
                />
            </div>
            <div class="p-10">
                <div class="text-xl dark:text-lavender-mist text-oil">
                    <slot name="title" />
                </div>
                <div class="dark:text-lavender-mist text-sm text-oil pt-2.5 pt-9">
                    <slot name="content" />
                </div>
                <slot name="footer" />
            </div>
        </div>
    </Modal>
</template>
<script>
    import Modal from './Modal.vue';
    import Icon from './Icon.vue'
    import { ref } from 'vue';

    export default {
        components: {
            Modal,
            Icon
        },
        props: {
            show: {
                type: Boolean,
                default: false,
            },
            maxWidth: {
                type: String,
                default: '2xl',
            },
            closeable: {
                type: Boolean,
                default: true,
            },
            colors: {
                type: Object,
                required: false
            }
        },
        emits: ['close'],
        setup(props, {emit}) {
            const close = () => {
                emit('close');
            };
            
            return {
                close,
            }
        }
    }    
</script>