import { getQuery ,getBody } from "h3";
import { queryByCollection } from "../../lib/firestore";

export default defineEventHandler(async (event) => {
  try {
    const query = getQuery(event);
    const docs = await queryByCollection(query.col);
    return { result: docs };
  } catch (error) {
    return { result: [], error: error.message };
  } 
});
