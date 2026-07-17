import { ref, onValue } from "firebase/database";
import { database } from "./firebase";

const occupancyRef = ref(database, "occupiedTents");

onValue(occupancyRef, (snapshot) => {
    // console.log(snapshot.val());
});
