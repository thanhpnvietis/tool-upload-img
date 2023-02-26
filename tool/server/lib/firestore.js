import {
  collection,
  getDocs,
  getDoc,
  addDoc,
  deleteDoc,
  doc,
  query,
  where,
  setDoc,
  collectionGroup,
  getFirestore,
  updateDoc,
  Timestamp,
} from "firebase/firestore";
import { firestoreDb,firebase } from "./firebase";
export const queryByCollection = async (col) => {
  const colRef = collection(firestoreDb, col);

  const snapshot = await getDocs(colRef);

  const docs = Array.from(snapshot.docs).map((doc) => {
    return {
      ...doc.data(),
      id: doc.id,
    };
  });

  return docs;
};

export const queryInclude = async (col,inColumn,value) => {
  const colRef = collection(firestoreDb, col);

  const q = query(colRef, where(inColumn, 'in', value));

  const snapshot = await getDocs(q);

  const docs = Array.from(snapshot.docs).map((doc) => {
    return {
      ...doc.data(),
      id: doc.id,
    };
  });
  return docs;
};

export const first = async (col,column,value) => {
  const colRef = collection(firestoreDb, col);

  const q = query(colRef, where(column, '==', parseInt(value)));

  const snapshot = await getDocs(q);

  const docs = Array.from(snapshot.docs).map((doc) => {
    return {
      ...doc.data(),
      id: doc.id,
    };
  });
  return docs[0];
};

export const set = async (col, document) => {
  await setDoc(doc(collection(firestoreDb, col)), document, { merge: true });
};
export const updateById = async (col,id, data) => {
    const collectionRef = doc(firestoreDb, col, id);

    await setDoc(collectionRef, data, { merge: true });

};


export const add = async (col, document) => {
  const colRef = collection(firestoreDb, col);

  const docRef = await addDoc(colRef, document);

  return docRef;
};

export const del = async (col, id) => {
  const docRef = doc(firestoreDb, col, id);
  return await deleteDoc(docRef);
};
