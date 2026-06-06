import './bootstrap';
import Alpine from 'alpinejs';

import navbar from './components/navbar';
import heroSlider from './components/heroSlider';
import animateOnLoad from './components/animateOnLoad';
import statsSection from './components/statsSection';
import featuresSection from './components/featuresSection';

window.Alpine = Alpine;

Alpine.data('navbar', navbar);
Alpine.data('heroSlider', heroSlider);
Alpine.data('animateOnLoad', animateOnLoad);
Alpine.data('statsSection', statsSection);
Alpine.data('featuresSection', featuresSection);

Alpine.start();
