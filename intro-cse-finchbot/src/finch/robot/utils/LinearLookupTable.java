package finch.robot.utils;

/**
 * Created by Yoseph Alabdulwahab on 3/29/2016.
 * This is a lookup table class. It is designed for storing multiple points and being able to interpolate
 * the best estimate based on given points. It is useful for real world applications when trying to simlate
 * something in real life without complex functions. A lookup table can be used to make accurate predictions
 * based on experimentation.
 */
public class LinearLookupTable {

    private double[] xArr;
    private double[] yArr;

    /**
     * Initializes an empty LinearLookupTable. It will make the table contain 0 entries.
     */
    public LinearLookupTable() {
        this(null, null);
    }

    /**
     * Initializes a linear interpolation lookup table.
     *
     * @param x the array of x values for this table.
     * @param y the array of y values for this table.
     */
    public LinearLookupTable(double[] x, double[] y) {
        if (x == null)
            x = new double[0];
        if (y == null)
            y = new double[0];
        if (x.length != y.length)
            throw new RuntimeException("Could not create a lookup table with different number of x and y values.");
        this.xArr = x;
        this.yArr = y;
    }

    /**
     * Adds a point to the lookup table. It will add it in the order that x value comes
     * to maintain order in the lookup table.
     * If a point with the same x value already exists then it will be replaced with the
     * new y value.
     *
     * @param x The X value that is desired to add to the lookup table
     * @param y The Y value that is desired to be associated with the given x value.
     */
    public void add(double x, double y) {
        int ix = find(xArr, x);
        if (xArr[ix] == x) {
            yArr[ix] = y;
            return;
        }
        double[] xAr = new double[this.xArr.length + 1];
        double[] yAr = new double[this.yArr.length + 1];
        int i = xAr.length - 1;
        while (i >= 0) {
            if (i == ix) {
                xAr[i] = x;
                yAr[i] = y;
            } else if (i > ix) {
                xAr[i] = this.xArr[i - 1];
                yAr[i] = this.yArr[i - 1];
            } else {
                xAr[i] = this.xArr[i];
                yAr[i] = this.yArr[i];
            }
            i--;
        }
        this.xArr = xAr;
        this.yArr = yAr;
    }

    public boolean remove(double x) {
        int ix = find(xArr, x);
        if (xArr[ix] != x)
            return false;
        double[] xAr = new double[this.xArr.length - 1];
        double[] yAr = new double[this.yArr.length - 1];
        int i = xAr.length - 1;
        while (i >= 0) {
            if (i >= ix) {
                xAr[i] = this.xArr[i + 1];
                yAr[i] = this.yArr[i + 1];
            } else {
                xAr[i] = this.xArr[i];
                yAr[i] = this.yArr[i];
            }
            i--;
        }
        this.xArr = xAr;
        this.yArr = yAr;
        return true;
    }

    /**
     * @param x the x value that is being searched for.
     * @return the linearly interpolated y value based on the entries.
     */
    public double getEstimate(double x) {
        int b = find(xArr, x);
        int a = b - 1;
        if (xArr.length == 1) {
            a = 0;
            b = 0;
        } else if (b <= 0) {
            b = 1;
            a = 0;
        } else if (b >= xArr.length - 1) {
            b = xArr.length - 1;
            a = b - 1;
        }
        double slope = (yArr[b] - yArr[a]) / (xArr[b] - xArr[a]);
        double y = slope * (x - xArr[a]) + yArr[a];
        return y;
    }

    /**
     * Gets the y value of which has an x value closest to the input.
     *
     * @param x the x value that is being searched for.
     * @return the y value of the entry whose x is closer to the input.
     */
    public double getClosestValue(double x) {
        int b = find(xArr, x);
        int a = b - 1;
        if (xArr.length == 1) {
            a = 0;
            b = 0;
        } else if (b <= 0) {
            b = 1;
            a = 0;
        } else if (b >= xArr.length - 1) {
            b = xArr.length - 1;
            a = b - 1;
        }
        if (b <= 0 || b >= xArr.length - 1)
            return yArr[b];
        if (Math.abs(Math.abs(xArr[a]) - Math.abs(x)) > Math.abs(Math.abs(xArr[b]) - Math.abs(x)))
            return yArr[a];
        return yArr[b];
    }

    /**
     * Returns the location of that element, or the location of the next biggest element.
     *
     * @param a   the specified array.
     * @param key the value desired to search for.
     * @return the index of the given value or the next biggest value.
     */
    private int find(double[] a, double key) {
        int imin = 0, imax = a.length - 1;
        int imid = imin + (imax - imin) / 2;
        while (imin <= imax) {
            imid = imin + (imax - imin) / 2;
            if (a[imid] == key)
                return imid;
            else if (a[imid] < key)
                imin = imid + 1;
            else
                imax = imid - 1;
        }
        if (imin > a.length - 1)
            imin = a.length - 1;
        else if (imin < 0)
            imin = 0;

        return imin;
    }

    /**
     * This array of x values from the lookup table corespond with the
     * indecies of y values in the array of y values in the lookup tbale.
     * For example, a value at index 3 in the x array of values would
     * correspond with the value at index 3 of the y array of values.
     *
     * @return The array of x values in the lookup table.
     */
    public double[] getXArr() {
        return xArr;
    }

    /**
     * This array of y values from the lookup table corespond with the
     * indecies of x values in the array of x values in the lookup tbale.
     * For example, a value at index 3 in the y array of values would
     * correspond with the value at index 3 of the x array of values.
     *
     * @return The array of y values in the lookup table.
     */
    public double[] getYArr() {
        return yArr;
    }
}
