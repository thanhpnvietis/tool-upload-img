// Import the functions you need from the SDKs you need


import { initializeApp } from 'firebase/app';
import { getFirestore, collection, getDocs } from 'firebase/firestore';
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyBdJ2c_GepDCE5Z9Wp0SNw2XuNNOSI8tYI",
  authDomain: "cobalt-carver-230002.firebaseapp.com",
  projectId: "cobalt-carver-230002",
  storageBucket: "cobalt-carver-230002.appspot.com",
  messagingSenderId: "847838353440",
  appId: "1:847838353440:web:06aea233f60ffab32e4445"
};

// Initialize Firebase
export const firebaseApp = initializeApp(firebaseConfig);
export const firestoreDb = getFirestore(firebaseApp);

