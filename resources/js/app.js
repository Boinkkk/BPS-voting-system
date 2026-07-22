import Swal from 'sweetalert2';
window.Swal = Swal;

import TomSelect from 'tom-select';
window.TomSelect = TomSelect;

import Cropper from 'cropperjs';
window.Cropper = Cropper;

import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;
