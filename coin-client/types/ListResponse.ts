export default interface ListResponse<T> {
  data: T[];
  message: string;
  status: number;
}
