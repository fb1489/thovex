import axios from 'axios';

const tokenElement = document.querySelector('meta[name="csrf-token"]');
if (tokenElement) {
    const token = tokenElement.getAttribute('content');
    axios.defaults.headers.common['X-CSRF-Token'] = token;
}

export default axios;
