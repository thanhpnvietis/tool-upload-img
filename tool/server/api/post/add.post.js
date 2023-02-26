import {  readBody,getQuery} from "h3";
import { add,first,updateById } from "../../lib/firestore";
export default defineEventHandler(async (event) => {
  try {
    const query = getQuery(event);
    const body = await readBody(event);
    const find = await first(query.col ,'post_id',body.post_id);
    if(find){
      await updateById(query.col,find.id,body);
    }else{
      await add(query.col , body);
    }
    const result = await first(query.col ,'post_id',body.post_id);
    return { result };
  } catch (error) {
    return { error: error.message }
  } 
});
