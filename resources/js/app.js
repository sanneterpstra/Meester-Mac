import './bootstrap';
import { Datepicker } from 'vanillajs-datepicker';
import 'vanillajs-datepicker/css/datepicker.css';
import nl from 'vanillajs-datepicker/locales/nl';

Object.assign(Datepicker.locales, nl);

import Alpine from 'alpinejs'
 
window.Datepicker = Datepicker;
window.Alpine = Alpine

Alpine.start()
