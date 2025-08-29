export type Availability = {
  date: string;
  slots: Array<Slot>;
};
export type Employee = {
  id: number;
  name: string;
  slug: string;
  avatar_url: string;
};
export type Service = {
  id: number;
  title: string;
  slug: string;
  duration: string;
  price: string;
};
export type Slot = {
  datetime: string;
  time: string;
  employees: { [key: number]: number };
};
