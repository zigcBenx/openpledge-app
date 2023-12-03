import { useToast } from "vue-toastification";

const toast = useToast();

export default {
  success(message) {
    toast.success(message);
  },
  error(message) {
    console.log('error inside')
    toast.error(message);
  },
  // Add other methods as needed
};